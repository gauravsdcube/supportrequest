<?php

use humhub\components\Migration;

class uninstall extends Migration
{
    public function up()
    {
        $this->safeDropTable('requestsupport_response');
        $this->safeDropTable('requestsupport_request');
        $this->safeDropTable('requestsupport_category');
    }

    public function down()
    {
        echo "uninstall does not support migration down.\n";
        return false;
    }

}
