-- Run this once in phpMyAdmin (or via mysql CLI) against your BMIS database.
-- Adds `region` and `province` text columns next to the existing `brgy` /
-- `municipal` columns, on both the pending and approved resident tables.

ALTER TABLE `tbl_resident_pending`
    ADD COLUMN `region` VARCHAR(100) NULL AFTER `street`,
    ADD COLUMN `province` VARCHAR(100) NULL AFTER `region`;

ALTER TABLE `tbl_resident`
    ADD COLUMN `region` VARCHAR(100) NULL AFTER `street`,
    ADD COLUMN `province` VARCHAR(100) NULL AFTER `region`;
