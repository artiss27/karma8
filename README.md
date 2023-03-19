# karma8

1. Download the project from Git.
2. In the .env file, you can specify the desired number of test users to be generated ```CNT_USERS```.
3. Run the command in the terminal from the root of the project: ```make build``` (This will do the necessary setup and generate test data).
4. Now the project is available on the local computer at: https://localhost/
5. Read task execution description and run tests.
6. Test data regeneration ```make faker-migrations```.
7. Stop project ```make down```.
8. launch the project ```make up```.
