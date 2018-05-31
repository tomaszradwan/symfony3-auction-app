<?php
/**
 * Created by PhpStorm.
 * User: tomasz
 * Date: 2018-05-31
 * Time: 20:48
 */

namespace AppBundle\EventDispatcher;


class Events
{
    const AUCTION_ADD = 'auction_add';
    const AUCTION_EDIT = 'auction_edit';
    const AUCTION_DELETE = 'auction_delete';
    const AUCTION_FINISH = 'auction_finish';
    const AUCTION_EXPIRE = 'auction_expire';
}
