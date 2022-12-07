<?php
/**
 * Conditionally delete a response on submission
 *
 * @author Adam Zammit <adam@acspri.org.au>
 * @copyright 2022 ACSPRI <https://www.acspri.org.au>
 * @license GPL v3
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 */

class DeleteOnSubmit extends PluginBase
{
    protected $storage = 'DbStorage';
    static protected $name = 'DeleteOnSubmit';
    static protected $description = 'Conditionally delete a response on submission';

    public function init()
    {
        $this->subscribe('beforeSurveySettings');
        $this->subscribe('newSurveySettings');
        $this->subscribe('afterSurveyComplete'); 
    }


    protected $settings = [
        'bDebugMode' => [
            'type' => 'select',
            'options' => [
                0 => 'No',
                1 => 'Yes'
            ],
            'default' => 0,
            'label' => 'Enable Debug Mode',
            'help' => 'Enable debugmode to check how expression is working'
        ]
    ];

    /**
     * Save the settings
     */
    public function newSurveySettings()
    {
        $event = $this->event;
        foreach ($event->get('settings') as $name => $value) {
            /* In order use survey setting, if not set, use global, if not set use default */
            $default = $event->get($name, null, null, $this->settings[$name]['default'] ?? null);
            $this->set($name, $value, 'Survey', $event->get('survey'), $default);
        }
    }


    /**
     * If result return is enabled - send a result back
     */
    public function afterSurveyComplete()
    {
        $event = $this->event;
        $surveyId = $event->get('surveyId');

        $rr = $this->get('sDeleteExpression', 'Survey', $surveyId);

        if (!empty($rr)) { //determine if we should delete the response
            $survey = Survey::model()->findByPk($surveyId);
            $responseId = $event->get('responseId');
            $response = $this->api->getResponse($surveyId, $responseId);
            $token = Token::model($surveyId)->findByToken($response['token']);
            $pr = LimeExpressionManager::ProcessString($rr, null, array(), 3, 1, false, false, true);
            $this->debug("Result of expression manager", $pr, microtime(true));
            if ($pr === true || $pr === "1") {
                $this->api->removeResponse($surveyId, $responseId); 
            }
        }
    }


    /**
     * Settings on survey level - what item to delete response on
     */
    public function beforeSurveySettings()
    {
        $event = $this->event;
        $survey = Survey::model()->findByPk($event->get('survey'));

        $sets = [
            'sDeleteExpression' => [
                'type' => 'string',
                'label' => 'The expression that will determine if a response should be deleted. Should return 1 or true if the response should be deleted',
                'help' => 'For example, {A1==\'Y\'} will delete a response if the answer to A1 is "Y". Setting to: 1 will always delete a response. Leaving blank will do nothing',
                'current' => $this->get('sDeleteExpression', 'Survey', $event->get('survey')),
            ]
        ];

        $event->set("surveysettings.{$this->id}", [
            'name' => get_class($this),
            'settings' => $sets
        ]);
    }

    private function debug($parameters, $hookSent, $timeStart)
    {
        if ($this->get('bDebugMode', null, null, $this->settings['bDebugMode'])) {
            echo '<pre>';
            var_dump($parameters);
            echo '<br><br> ----------------------------- <br><br>';
            var_dump($hookSent);
            echo '<br><br> ----------------------------- <br><br>';
            echo 'Total execution time in seconds: ' . (microtime(true) - $timeStart);
            echo '</pre>';
        }
    }
}
