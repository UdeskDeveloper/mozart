<?php
/**
 * Copyright 2014 Alexandru Furculita <alex@rhetina.com>
 */

namespace Mozart\Bundle\TaxonomyBundle;


interface TaxonomyInterface {
    public function getName();
    public function getObjectTypes();
    public function getLabels();
    public function isPublic();
    public function showUI();
    public function showInNavMenus();
    public function showTagCloud();
    public function getMetaboxCallback();
    public function showAdminColumn();
    public function isHierarchical();
    public function getUpdateCountCallback();
    public function getQueryVariable();
    public function isBuiltin();
    public function toSort();
    public function getCapabilities();
    public function getRewriteOptions();
} 