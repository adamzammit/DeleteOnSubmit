# DeleteOnSubmit
LimeSurvey Plugin that allows you to specify an equation that will determine if a response should be deleted on submission. This plugin can be used if you wish to provide some private feedback to respondents (e.g. display a report) but give the option for respondents to not keep their data.

## Installation

Download the zip from the [releases](https://github.com/adamzammit/DeleteOnSubmit/releases) page and extract to your plugins folder. You can also clone directly from git: go to your plugins directory and type
```
git clone --recursive https://github.com/adamzammit/DeleteOnSubmit.git DeleteOnSubmit
```

## Requirements

- LimeSurvey version 3.x, 4.x, 5.x

## Configuration (LimeSurvey)

1. Visit the "Plugin manager" section in your LimeSurvey installation under "Configuration"
2. Choose if you want the plugin to be enabled or not
3. Save the settings
4. Activate the plugin
5. Activate an existing or new survey
6. Visit "Simple plugin settings" for the survey and choose "Settings for plugin DeleteOnSubmit"
7. Choose what expression should determine if a response should be deleted, eg A1=='Y' would be if question A1 is answered "Y", the response should be deleted. You can just put the number 1 if you want all responses deleted regardless of the choice of the respondent.

Note: See the included file "example-survey-deleteonsubmit.lss" to see how you can set up a question to decide whether to delete on submit or not

## Data loss warning

Stating the obvious: Warning - this plugin is designed to delete responses - so loss of responses is a design feature.

## Security

If you discover any security related issues, please email adam@acspri.org.au instead of using the issue tracker.

## Contributing

PR's are welcome!

## Usage

You are free to use/change/fork this code for your own products (licence is GPLv3), and I would be happy to hear how and what you are using it for!
