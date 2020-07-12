<?php

return [
    'product_category' => [
        'error' => [
            'none_record' => 'Danh mục sản phẩm không tồn tại.',
        ]
    ],
    'campaign'         => [
        'error' => [
            'now_date'            => 'Ngày bắt đầu và kết thúc phải lớn hơn hôm nay.',
            'allow_status_delete' => 'Ban không thể xóa được chiến dịch này.',
            'allow_status_edit'   => 'Bạn không thể chỉnh sửa được chiến dịch này.',
            'none_exists'         => 'Không tìm thấy chiến dịch.'
        ],
        'info'  => [
            'default_name' => 'Chiến dịch'
        ],
        'excel' => [
            'header_col' => [
                'Mã KH', 'Tên Khách Hàng', 'Tên Nhân Viên', 'Mã Nhân Viên'
            ]
        ]
    ],
    'product'          => [
        'error' => [
            'none_record'    => 'Sản phẩm không tồn tại.',
            'prevent_delete' => 'Hiện tại có 1 hoặc nhiều chiến dịch đang chạy cho sản phầm này. Bạn không thể xóa được sản phẩm này!',
        ]
    ],
    'daily_task'       => [
        'error'   => [
            'file_valid'       => 'Định dạng tập tin không hợp lệ.',
            'error_import'     => 'Không nhập được danh sách công việc định kỳ. <br> Xin vui lòng thử lại.',
            'error_valid_data' => 'Dữ liệu không đầy đủ thông tin'
        ],
        'success' => [
            'import_success' => 'Nhập công việc định kỳ thành công.'
        ]
    ],
    'general'          => [
        'error' => [
            'none_record' => 'Dữ liệu không được tìm thấy.',
        ]
    ],
    'ticket'           => [
        'category' => [
            'error' => [
                'none_exists' => 'Không tìm thấy danh mục ticket.',
                'none_record' => 'Không tìm thấy danh mục ticket.'
            ]
        ],
        'error' => [
            'none_exists' => 'Không tìm thấy ticket.',
            'none_record' => 'Không tìm thấy ticket.'
        ]
    ]
];
