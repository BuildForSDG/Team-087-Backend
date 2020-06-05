# Mental.ly \[API]

[![Codacy Badge](https://api.codacy.com/project/badge/Grade/07ff081ef79845559db9a4fd5e77e760)](https://app.codacy.com/gh/BuildForSDG/Team-087-Backend?utm_source=github.com&utm_medium=referral&utm_content=BuildForSDG/Team-087-Backend&utm_campaign=Badge_Grade_Settings)
[![Build Status](https://travis-ci.com/BuildForSDG/Team-087-Backend.svg?branch=develop)](https://travis-ci.com/BuildForSDG/Team-087-Backend)

## About

<!-- What is this project about. Ok to enrich here or the section above it with an image. -->

This project is about the backend services (APIs) that power the effort at creating a platform to assist `mental-health patients` in accessing proper care by providing relevant recommendations on `mental-health specialists` around them as well as `care-groups` for better closure.

<!-- Once this repo has been setup on Codacy by the TTL, replace the above badge with the actual one from the Codacy dashboard, and add the code coverage badge as well. This is mandatory -->

This setup of this project contains:

-   `composer`: For adding third party dependencies

-   `phpunit`: For runnung tests

-   `php-cs-fixer`: For formatting code to match php coding standard
    <br><br>

## Why

<!-- Talk about what problem this solves, what SDG(s) and SGD targets it addresses and why these are important -->

In the midst of a pandemic such as we have around the world today with the **`COVID-19`** outbreak, many people would have been set into depression. As a result, mental-health issues are likely to become the order of the day post-pandemic. It is important that the right care is accessible to prospective patients of different age-demographic amidst other preferences/factors. So, this project aims to address the **`Problem 1`** of **`Goal-3`** of the **`SDGs`** by building a platform where relevant recommendation(s) can be made and proper care can be accessed within the vicinity or closest locality of the prospective mental-health patient.
<br><br>

## Usage

<!-- How would someone use what you have built, include URLs to the deployed app, service e.t.c when you have it setup -->

This backend project can be accessed via <https://mental-ly.herokuapp.com>.<br>
The counterpart frontend project can be accessed via <https://mental-lyf.herokuapp.com>.<br>
The link to this project's postman collection will be provided at the end of the on-going development phase.
<br><br>

## Setup

<!-- The `index.php` is the entry to the project and source code should go into the `src` folder. All tests should be written in the test folder. -->

-   Clone/Download the project and set it up in the document-root of the local server in your PC
-   Run `composer install` to install all the application's composer dependencies
-   Run `composer dump-autoload` to get all the application's classes aggregated into a class-map for easy referencing
-   Set random characters to the `APP_KEY` environment variable
-   Run `php artisan jwt:secret` for generation of the **`JWT-SECRET`** value
-   Voila! The app's services are all setup locally!

**Note:** _(In order to enjoy the functionalities in this project, kindly follow the instructions on its accompanying [frontend](https://github.com/BuildForSDG/Team-087-Frontend) project to setup the UI._
<br><br>

## Running the application

-   Start your local server running Apache/Nginx and Postgresql as the database-server
-   Create a **database** and set its name to the value of the **`DB_DATABASE`** in your **`.env`** file within your project's root. (**_This is done on first-run ONLY_**)
-   Run **`php artisan migrate`** to setup the tables used by the app within the database you just created. Additionally, if you will like to have sample data in the tables, you may append the `--seed` flag like so **`php artisan migrate --seed`**. (**_This is done on first-run ONLY_**)

-   Access the app locally at `http://{SERVER-NAME-OR-IP}[:{PORT-NUMBER}]/` where `SERVER-NAME-OR-IP` is your local web-server IP/Name or virtual-host name (usually **_localhost_**) and `PORT-NUMBER` (optional) is the port-number on which your local web-server (i.e. Apache/Nginx) runs.

### Useful Hints

-   Test: `composer run test`

-   Install dependencies: `composer require <dep-name>`

-   Lint: `composer run php-cs-fixer`
    <br><br>

## Authors

<!-- List the team behind this project. Their names linked to their Github, LinkedIn, or Twitter accounts should siffice. Ok to signify the role they play in the project, including the TTL and mentor -->

<!-- ### Team-087 Members -->

-   [Eze Promise](https://github.com/Code-panther) - **`Backend`**

-   [Harcourt Hamsa](https://github.com/harcourthamsa) - **`Frontend`**

-   [Chinweike David](https://github.com/daveOnactive) - **`Frontend`**

-   [Emma NWAMAIFE](https://github.com/highman95) - **`Backend/TTL`**

-   [Edafe](https://github.com/JohnMadakin) - **`Mentor`**
    <br><br>

## Contributing

If this project sounds interesting to you and you'd like to contribute, thank you!
First, you can send a mail to <buildforsdg@andela.com> to indicate your interest, why you'd like to support and what forms of support you can bring to the table, but here are areas we think we'd need the most help in this project :

1.  area one (e.g this app is about `mental-health patients` and the `specialists`/service-providers in this health-sector and you need feedback on your roadmap and feature list from the private sector / NGOs)

2.  area two (e.g you want people to opt-in and try using your staging app at <https://mental-ly.herokuapp.com> and report any bugs via a form)

3.  area three (e.g here is the zoom link to our end-of sprint webinar, join and provide feedback as a stakeholder if you can)
    <br><br>

## Acknowledgements

<!-- Did you use someone else’s code?
Do you want to thank someone explicitly?
Did someone’s blog post spark off a wonderful idea or give you a solution to nagging problem?

It's powerful to always give credit. -->

Special Thanks to [Sean Tymon](https://github.com/tymondesigns) for the JWT-auth library that drives the authentication strategy of this project.
<br><br>

## License

MIT
