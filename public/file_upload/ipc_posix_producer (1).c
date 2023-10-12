/*
Interprocess communication using shared memory.
Run this producer code before running the consumer 
code in the separate terminal tab.

Taken from textbook OSC10

Edited by:
Heri Kurniawan
*/

#include <stdio.h>
#include <stdlib.h>
#include <string.h>
#include <fcntl.h>
#include <sys/shm.h>
#include <sys/stat.h>
#include <unistd.h> 
#include <sys/mman.h>
 
int main()
{
    /* the size (in bytes) of shared memory object */
    const int SIZE = 4096;
    /* name of the shared memory object */
    const char *name = "OS";
    /* strings written to shared memory */
    const char *message_0 = "\nHello";
    const char *message_1 = "World!\n";
     
    /* shared memory file descriptor */
    int fd;
    /* pointer to shared memory obect */
    char *ptr;
       /* create the shared memory object */
    fd = shm_open(name,O_CREAT | O_RDWR,0666);

    /* configure the size of the shared memory object */
    ftruncate(fd, SIZE);

    /* memory map the shared memory object */
    ptr = (char *)
    mmap(0, SIZE, PROT_READ | PROT_WRITE, MAP_SHARED, fd, 0);

    /* write to the shared memory object */
    sprintf(ptr,"%s",message_0);
    ptr += strlen(message_0);
    sprintf(ptr,"%s",message_1);
    ptr += strlen(message_1);
    sleep(5);

    return 0;
}
