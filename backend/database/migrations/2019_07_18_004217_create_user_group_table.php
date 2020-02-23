<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserGroupTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_group', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name',20)->unique()->comment('用户组名称');
            $table->tinyInteger('status')->default(0)->comment('状态:0 正常，1 冻结');
            $table->timestamps();
        });

        $this->_permission();
    }

    private function _permission()
    {
        $row = DB::table('admin_role_permissions')->where('name', '会员管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        $users_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $row->id,
            'rule'        => 'user_group/',
            'name'        => '用户组管理',
            'extra'       => json_encode(['icon' => 'user_group','component'=>'SubPage','redirect'=>'/user_group/index']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $users_id,
                'rule'        => 'user_group/index',
                'name'        => '用户组列表',
                'extra'       => json_encode(['hidden' => true,'component'=>'user_group/index']),
            ],
            [
                'parent_id'   => $users_id,
                'rule'        => 'user_group/create',
                'name'        => '增加用户组',
                'extra'       => json_encode(['hidden' => true]),
            ],
            [
                'parent_id'   => $users_id,
                'rule'        => 'user_group/delete',
                'name'        => '删除用户组',
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
        Schema::dropIfExists('user_group');
    }
}
