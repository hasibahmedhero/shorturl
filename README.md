
# Short URL Generator

A short URL generator application build with laravel and vuejs


## Features

- Creaets a short unique URL for a user provided long URL.
- Redirects visitor to the long URL when visiting the generated short URL.
- The URL is shortened till 6 symbols hash, which contains alphanumeric symbols.
- Algorithm recognizes duplicate URL and instead of generating new short URL, shows previously created URL.
- The URL is checked using "Google Safe Browsing" API to prevent unsafe URL visiting.
- Checking if the user provided URL is a valid URL.
- Copy to clipboard option for generated short URL.
- Stores recent history in localstorage and shows to user.


## Installation

- Clone the repository.
- Create MySQL database and import dump file "shorturl.sql" to the database.
- Add database credentials in the .env file.
- Add GOOGLE_SAFE_BROWSING_API_KEY and GOOGLE_SAFE_BROWSING_API_CLIENT_ID in the .env file.


## Demo

http://139.59.114.17:8099/
