/*
This program creates a process using fork() 
and then replace it with a new process image using execlp().

Taken from textbook OSC10

Edited by:
Heri Kurniawan
*/

#include <sys/wait.h>
#include <stdio.h>
#include <unistd.h>
 
int main()
{
pid_t pid;
 
   /* fork a child process */
   pid = fork();
 
   if (pid < 0) { /* error occurred */
     fprintf(stderr, "Fork Failed");
     return 1;
   }
   else if (pid == 0) { /* child process */
     execlp("/bin/ls","ls",NULL);
   }
   else { /* parent process */
     /* parent will wait for the child to complete */
     wait(NULL);
     printf("Child Complete");
   }
 
   return 0;
}
