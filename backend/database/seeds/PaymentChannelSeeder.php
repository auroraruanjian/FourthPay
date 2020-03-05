<?php

use Illuminate\Database\Seeder;

class PaymentChannelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        DB::table('payment_channel')->insert([
            [
                'name'                  => '微信测试商户',
                'payment_category_id'   => 1,
                'channel_param'         => json_encode([
                    'appid'         => 'wx342342424',
                    'merchant_id'   => '11111111',
                    'key1'          => '99754106633f94d350db34d548d6091a']),
                'status'                => 0,
            ],
            [
                'name'                  => '支付宝测试商户',
                'payment_category_id'   => 2,
                'channel_param'         => json_encode([
                    'appid'         => 'wx342342424',
                    'key1'          => 'MIGfMA0GCSqGSIb3DQEBAQUAA4GNADCBiQKBgQDdT7677GQG1zMvtSNHQBZBhlDDuQ8Vp96i6ArUCs9dHHhcjf4ZVLt6uExcqq+ehZ8+9die+5XHy/kdix2uuGP3lFDq8r3DDC2lKw5GRZNmNhm/uYtuUoEdrfVOKy+8CvYgXwkdhFV/bFn3gmnnS6XHZOdjYZpzEtfe9nFcfESh4wIDAQAB',
                    'key2'          => 'MIICdwIBADANBgkqhkiG9w0BAQEFAASCAmEwggJdAgEAAoGBAN1PvrvsZAbXMy+1I0dAFkGGUMO5DxWn3qLoCtQKz10ceFyN/hlUu3q4TFyqr56Fnz712J77lcfL+R2LHa64Y/eUUOryvcMMLaUrDkZFk2Y2Gb+5i25SgR2t9U4rL7wK9iBfCR2EVX9sWfeCaedLpcdk52NhmnMS1972cVx8RKHjAgMBAAECgYBBwT/adhICNk5vDlK9CL2UGmREwudId+geXvNYrR2PfLYGSeoFfLd3burBXbVwVCNMgZ8rHXUTN/d8X6kiEIciqtzFwfzMY9B+AO6i6app8fltMDV3XFsqrCiulNRQx7fITFuCY/QVaEsoennoKcBHVuu1aAGeKgEtQRx0zAS+AQJBAPVJIBpIU3ZD20/yINB4KHcxIQIROVUEDcCVTT2FptfGn0Nr5WNBosJEw94dVbQFEkOwPWfCPrrUthxy/QaMy5MCQQDm+oguCfd3Ph3EpXTcoqlIpVFj7qSp8e0fZR8Kp7H3H06RkoH4ZO9u91JHgH3FX7GrYKLggoJxQ26opK9IXeJxAkA9pWY7FMhdJcq9ufXDTx+kbSWFlnyZQE6QgzWJa8i13EZ0tG5PVtSoDI5JiYfbmZawil+0bx5C6Gjol4G+Hq6fAkEAp53xiy9yZRcvtQ+5AZANNwm5ScLZvfYxdNIGN85AKKyQcLWYGfDtjGJwVDEblEsAfPSQ3kn3bUhZGosAJIz1EQJBAKgiO1kgcpXVsWTfSlHcwq8zysYc/AsGqAYq5wLHhoOYbEpPXcmSLhP5fbBpYo3nGtyxMooFq8nvZQEYyZ5f22o='
                ]),
                'status'                => 0,
            ],
        ]);

        DB::table('payment_channel_detail')->insert([
            // 微信
            [
                'payment_channel_id'    => 1,
                'payment_method_id'     => 2,
                'rate'                  => 1,
                'min_amount'            => 1,
                'max_amount'            => 10000,
                'status'                => 1,
                'top_merchant_ids'      => json_encode([1]),
                'start_time'            => '00:00:00',
                'end_time'              => '00:00:00',
                'extra'                 => json_encode([]),
            ],
            [
                'payment_channel_id'    => 1,
                'payment_method_id'     => 7,
                'rate'                  => 1,
                'min_amount'            => 1,
                'max_amount'            => 10000,
                'status'                => 1,
                'top_merchant_ids'      => json_encode([1]),
                'start_time'            => '00:00:00',
                'end_time'              => '00:00:00',
                'extra'                 => json_encode([]),
            ],
            // 支付宝
            [
                'payment_channel_id'    => 2,
                'payment_method_id'     => 3,
                'rate'                  => 1,
                'min_amount'            => 1,
                'max_amount'            => 10000,
                'status'                => 1,
                'top_merchant_ids'      => json_encode([1]),
                'start_time'            => '00:00:00',
                'end_time'              => '00:00:00',
                'extra'                 => json_encode([]),
            ],
            [
                'payment_channel_id'    => 2,
                'payment_method_id'     => 8,
                'rate'                  => 1,
                'min_amount'            => 1,
                'max_amount'            => 10000,
                'status'                => 1,
                'top_merchant_ids'      => json_encode([1]),
                'start_time'            => '00:00:00',
                'end_time'              => '00:00:00',
                'extra'                 => json_encode([]),
            ]
        ]);
    }
}
