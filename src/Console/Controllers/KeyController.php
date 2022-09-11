<?php

declare(strict_types=1);

namespace Zae\IndexNow\Console\Controllers;

use craft\console\Controller;
use craft\helpers\StringHelper;
use yii\console\ExitCode;

class KeyController extends Controller
{
    /**
     * Generate a random key needed to push indexNow jobs.
     * @return int
     */
    public function actionGenerate(): int
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-';
        $this->stdout(StringHelper::randomStringWithChars($chars, 36) . PHP_EOL);

        return ExitCode::OK;
    }
}
