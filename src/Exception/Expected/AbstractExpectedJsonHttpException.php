<?php

namespace App\Exception\Expected;

use App\Exception\Api\JsonHttpException;

/**
 * Using in case, if cause of exception is not unusual situation, but expected behaviour
 * Child classes of this exception ignoring by Sentry.
 */
abstract class AbstractExpectedJsonHttpException extends JsonHttpException
{
}
