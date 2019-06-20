<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('config', function (Blueprint $table) {
            $table->smallIncrements('id')->comment('配置 ID');
            $table->SmallInteger('parent_id')->default(0)->comment('配置父 ID');
            $table->string('title', 64)->comment('配置标题');
            $table->string('key', 64)->unique()->comment('系统配置名称');
            $table->string('value', 256)->comment('系统配置值');
            // $table->smallInteger('input_type')->default(0)->comment('配置输入类型 0输入框，1下拉框，2复选框');
            // $table->smallInteger('value_type')->default(0)->comment('配置值 验证类型 0字符串，1数字，2大于零正数');
            // $table->string('input_option', 256)->default('')->comment('输入选项，当input_type 为下拉框或者复选框使用');
            $table->string('description', 128)->default('')->comment('配置描述');
            $table->boolean('is_disabled')->index()->default(0)->comment('配置项是否禁用 0:禁用 1:启用');
            $table->timestamps();
        });

        $this->_permission();
    }

    private function _permission()
    {
        $id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => 0,
            'rule'        => 'config',
            'name'        => '网站管理',
            'extra'       => json_encode(['icon' => 'config','component'=>'Layout']),
        ]);

        $config_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $id,
            'rule'        => 'config/index',
            'name'        => '配置管理',
            'extra'       => json_encode(['icon' => 'setting','component'=>'config/index']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $config_id,
                'rule'        => 'config/create',
                'name'        => '添加配置',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $config_id,
                'rule'        => 'config/edit',
                'name'        => '修改配置',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $config_id,
                'rule'        => 'config/delete',
                'name'        => '删除配置',
                'extra'       => json_encode(['hidden' => true]),
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('config');
    }
}
