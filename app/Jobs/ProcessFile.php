<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use App\Models\Product;
use App\Models\Upload;
use Error;
use Exception;
use Illuminate\Support\Facades\Log;
use League\Csv\Reader;

class ProcessFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    public $timeout = 1200;

    /**
     * Create a new job instance.
     */
    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * Execute the job.
     */

    private function splitMyArray(array $input_array, int $size, $preserve_keys = null): array
    {
        $nr = (int)ceil(count($input_array) / $size);
    
        if ($nr > 0) {
            return array_chunk($input_array, $nr, $preserve_keys);
        }
    
        return $input_array;
    }


    public function handle(): void
    {
        $record = Upload::find($this->data['id']);

        if ($record) {


            try {

                $csv = Reader::createFromPath($this->data['file_path'], 'r');
                $csv->setHeaderOffset(0);

                $desiredKeys = [
                    'UNIQUE_KEY',
                    'PRODUCT_TITLE',
                    'PRODUCT_DESCRIPTION',
                    'STYLE#',
                    'SANMAR_MAINFRAME_COLOR',
                    'SIZE',
                    'COLOR_NAME',
                    'PIECE_PRICE'
                ];

                $data = $csv->getRecords();

                $newArray = [];

                $existingArray = iterator_to_array($data);


                // Loop through the existing array
                foreach ($existingArray as $item) {

                    // Initialize an empty array for the filtered item
                    $filteredItem = [];

                    // Loop through the desired keys and filter them
                    foreach ($desiredKeys as $key) {
                        // Check if the key exists in the item
                        if (isset($item[$key])) {
                            // Add the key and its corresponding value to the filtered item
                            $filteredItem[$key] = mb_convert_encoding($item[$key], 'UTF-8', 'UTF-8');
                        }
                    }

                    // Add the filtered item to the new array
                    $newArray[] = $filteredItem;
                }


                $newArray2 = $this->splitMyArray($newArray, 1000);
                foreach($newArray2 as $submittedArray) {
                    Product::upsert($submittedArray, ['UNIQUE_KEY'], $desiredKeys);
                }

                $record->status = 'completed';
                $record->save();

                broadcast(new \App\Events\fileStatus([
                    "id" => $this->data['id'],
                    "status" => "completed",
                    "isNew" => false
                ]));


            } catch (Exception $e) {
                // If an exception is caught, update the status to "failed"
                $record->status = 'failed';
                $record->save();

                broadcast(new \App\Events\fileStatus([
                    "id" => $this->data['id'],
                    "status" => "failed",
                    "isNew" => false
                ]));

                // Handle the exception or log the error if needed
                Log::error("Error in job: " . $e->getMessage());
            }
        } else {
            Log::error("Record not found for hash: " . $this->data->hash);
        }
    }
}
