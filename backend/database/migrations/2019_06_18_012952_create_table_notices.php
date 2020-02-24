<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableNotices extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notices', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('subject', 64)->unique()->comment('标题');
            $table->text('content')->comment('内容');
            $table->smallInteger('created_admin_user_id')->default(0)->comment('创建管理ID');
            $table->smallInteger('verified_admin_user_id')->default(0)->comment('审核管理ID');
            $table->smallInteger('sort')->default(0)->comment('排序');
            $table->boolean('is_show')->default(false)->comment('是否显示');
            $table->boolean('is_top')->default(false)->comment('是否置顶');
            $table->boolean('is_alert')->default(false)->comment('是否弹出提示');
            $table->timestamp('created_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('创建时间');
            $table->timestamp('published_at')->default(DB::raw('LOCALTIMESTAMP'))->comment('发布时间');
            $table->timestamp('verified_at')->nullable()->comment('审核时间');

            $table->index(['is_show', 'sort']);
        });

        $this->_permission();
    }

    private function _permission()
    {
        $row = DB::table('admin_role_permissions')->where('name', '网站管理')->where('parent_id', 0)->first();

        if (empty($row)) {
            return;
        }

        $notice_id = DB::table('admin_role_permissions')->insertGetId([
            'parent_id'   => $row->id,
            'rule'        => 'notices/',
            'name'        => '公告管理',
            'extra'       => json_encode(['icon' => 'notice','component'=>'SubPage','redirect'=>'/notices/index']),
        ]);

        DB::table('admin_role_permissions')->insert([
            [
                'parent_id'   => $notice_id,
                'rule'        => 'notices/index',
                'name'        => '公告列表',
                'extra'       => json_encode(['hidden' => true,'component'=>'notices/index']),
            ],
            [
                'parent_id'   => $notice_id,
                'rule'        => 'notices/create',
                'name'        => '添加公告',
                'extra'       => json_encode(['hidden' => true,'component'=>'notices/create']),
            ],
            [
                'parent_id'   => $notice_id,
                'rule'        => 'notices/edit',
                'name'        => '修改公告',
                'extra'       => json_encode(['hidden' => true,'params'=>['id'],'component'=>'notices/edit']),
            ],
            [
                'parent_id'   => $notice_id,
                'rule'        => 'notices/delete',
                'name'        => '删除公告',
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
        Schema::dropIfExists('notices');
    }
}
