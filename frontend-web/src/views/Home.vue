<template>
  <div class="home">
    <img alt="Vue logo" src="../assets/logo.png">
    <HelloWorld msg="Welcome to Your Vue.js App"/>
  </div>
</template>

<script>
// @ is an alias to /src
import HelloWorld from '@/components/HelloWorld.vue'
import { getTest } from '@/api/test.js';

export default {
    name: 'Home',
    components: {
        HelloWorld
    },
    methods:{
        async getTest(){
            let result = await getTest();console.log(result);
            if( result.status == 200 ){
                this.$message({
                    message: '跨域接口请求测试成功，返回内容：' + JSON.stringify(result.data),
                    type: 'success'
                });
            }else{
                this.$message({
                    message: '接口请求测试失败，HTTP状态码：' + result.status + ' 返回内容：' + JSON.stringify(result.data),
                    type: 'error'
                });
            }
        },
    },
    created() {
        this.$message({
            message: '集成Element UI成功',
            type: 'success'
        });
        this.getTest();
    }
}
</script>
