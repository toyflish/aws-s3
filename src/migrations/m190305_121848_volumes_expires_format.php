<?php

namespace craft\awss3\migrations;

use Craft;
use craft\db\Migration;
use craft\awss3\Volume;
use craft\helpers\Json;
use craft\services\Volumes;

/**
 * m190305_121848_volumes_expires_format migration.
 */
class m190305_121848_volumes_expires_format extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $projectConfig = Craft::$app->getProjectConfig();

        $projectConfig->muteEvents = true;
        $volumes = $projectConfig->get(Volumes::CONFIG_VOLUME_KEY) ?? [];

        foreach ($volumes as $uid => &$volume) {
            if ($volume['type'] === Volume::class && isset($volume['settings']) && is_array($volume['settings']) && isset($volume['settings']['expires'])) {
                $volume['settings']['expires'] = preg_replace('/(\d+)(\w+)/i', '$1 $2', $volume['settings']['expires']);

                $this->update('{{%volumes}}', [
                    'settings' => Json::encode($volume['settings']),
                ], ['uid' => $uid]);

                $projectConfig->set(Volumes::CONFIG_VOLUME_KEY . '.' . $uid, $volume);
            }
        }

        $projectConfig->muteEvents = false;
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "m190305_121848_volumes_expires_format cannot be reverted.\n";
        return false;
    }
}
