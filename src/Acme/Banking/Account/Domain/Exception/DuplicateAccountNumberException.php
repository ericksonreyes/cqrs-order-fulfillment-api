<?php
/**
 * Created by PhpStorm.
 * User: ericksonreyes
 * Date: 2019-06-24
 * Time: 11:13
 */

namespace Acme\Banking\Account\Domain\Exception;

use EricksonReyes\DomainDrivenDesign\Common\Exception\RecordConflictException;

/**
 * Class DuplicateAccountNumberException
 */
final class DuplicateAccountNumberException extends RecordConflictException
{
}
