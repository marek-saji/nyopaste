<?php
/**
 * Profile pages
 */
interface IProfileController
{
    /**
     * Profile data fetched from db
     *
     * Comon keys:
     * - [DisplayName]
     * - [id]
     * - [ProfileType] same values as box.profile_type enum
     *
     * @return array
     */
    public function getRow();
}

