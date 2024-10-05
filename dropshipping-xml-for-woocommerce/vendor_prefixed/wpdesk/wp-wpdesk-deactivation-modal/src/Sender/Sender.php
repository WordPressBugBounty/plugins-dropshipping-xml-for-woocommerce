<?php

namespace DropshippingXmlFreeVendor\WPDesk\DeactivationModal\Sender;

use DropshippingXmlFreeVendor\WPDesk\DeactivationModal\Exception\SenderRequestFailedException;
use DropshippingXmlFreeVendor\WPDesk\DeactivationModal\Model\RequestData;
interface Sender
{
    /**
     * @param RequestData $request_data .
     *
     * @return array
     */
    public function generate_request_data(RequestData $request_data): array;
    /**
     * @param RequestData $request_data .
     *
     * @return bool Request success status.
     *
     * @throws SenderRequestFailedException
     */
    public function send_request(RequestData $request_data): bool;
}