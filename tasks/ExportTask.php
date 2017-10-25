<?php

namespace Craft;

/**
 * Power Nap task
 */
class ExportTask extends BaseTask
{
    protected $tempName = '';

    protected $data;

    /**
     * Gets the total number of steps for this task.
     *
     * @return int
     */
    public function getTotalSteps()
    {
        return 3;
    }

    /**
     * Defines the settings.
     *
     * @access protected
     * @return array
     */
    protected function defineSettings()
    {
        return array(
            'dataSettings' => AttributeType::Mixed,
            'emails' => AttributeType::String,
        );
    }

    /**
     * Runs a task step.
     *
     * @param int $step
     * @return bool
     */
    public function runStep($step)
    {
        if ($step == 2) {
            //Finally send an email
            $emails = explode(',', $this->getSettings()->emails);

            $email = new EmailModel();
            $email->subject = 'Your export';
            $email->addAttachment($this->getStoragePath(), 'Report.csv', 'base64', 'text/csv');
            $email->toEmail = array_shift($emails);

            if (count($emails)) {
                $email->cc = array_map(function($email) {
                    return [
                        'name' => 'Recipient',
                        'email' => $email
                    ];
                },$emails);
            }

            craft()->email->sendEmail($email);


            return true;
        } else if($step == 1) {
            //Write it to a temp file
            $this->tempName = (new \DateTime())->getTimestamp();

            file_put_contents($this->getStoragePath(), $this->data);
            return true;
        } else {
            //Build up a data variable
            $this->data = craft()->export->download($this->getSettings()->dataSettings);
            return true;
        }
    }

    /**
     * @return string
     */
    private function getStoragePath()
    {
        return __DIR__.'/storage/'.$this->tempName.'.csv';
    }
}