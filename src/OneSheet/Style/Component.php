<?php

namespace OneSheet\Style;

interface Component
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
