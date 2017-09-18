# denjob
Simple academic position advertisement web interface

This minimalistic web interface allows for Academic institutions to collect CV and cover letters for an open position.

Typically, this goes in 3 steps:
 - applicants provide contact details, resume, referees details
 - referees provide support letters
 - position assessors read the resumes and support letters and select a few candidates


# Features
 - No passwords involved, unique links are sent by email
 - All files are stored using hash names, preventing indiscretions
 - 4 different roles:
   o Applicants
   o Referees
   o Assessors
   o Chairman (admin)

# Setup
 - Copy config.php.sample to config.php
 - Change the HASH_SALT to something unguessable
 - On the command line, run *php chairman.php*: it will create the file for storing admin information
 - Edit the file created type one email address for each of the admin per line
 - Run *php chairman.php* again, this will send the links to the admins
 - Create new folder 'positions/$n/' where $n is a number
 - Create files 'positions/$n/title' and 'positions/$n/desc' for position Title and Description respectively
 - In the chairman interface, toggle the position as 'open'
 - Advertise your link to candidates
