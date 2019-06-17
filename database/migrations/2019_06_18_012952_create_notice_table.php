<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNoticeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notice', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('subject', 64)->unique()->comment('标题');
            $table->text('content')->comment('内容');
            $table->smallInteger('created_admin_user_id')->default(0)->comment('创造管理ID');
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

        DB::table('admin_role_permissions')->insert([
            'parent_id'   => $row->id,
            'rule'        => 'notice/index',
            'name'        => '公告管理',
            'extra'       => json_encode(['icon' => 'notice','component'=>'Layout']),
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notice');
    }
}
