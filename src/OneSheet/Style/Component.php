<?php
/**
 * @author neun
 * @since  2016-08-13
 */

namespace OneSheet\Style;

Interface Component
{
    /**
     * @param int $id
     */
    public function setId($id);

    /**
     * @return int
     */
    public function getId();

    /**
     * @return string
     */
    public function asXml();
}
