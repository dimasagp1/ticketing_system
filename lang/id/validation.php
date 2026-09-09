<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Baris Bahasa untuk Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut berisi standar pesan kesalahan yang digunakan oleh
    | kelas validasi. Beberapa aturan memiliki banyak versi seperti aturan ukuran.
    | Jangan ragu untuk mengubah setiap pesan di sini.
    |
    */

    'accepted'             => ':attribute harus diterima.',
    'accepted_if'          => ':attribute harus diterima ketika :other bernilai :value.',
    'active_url'           => ':attribute bukan URL yang valid.',
    'after'                => ':attribute harus tanggal setelah :date.',
    'after_or_equal'       => ':attribute harus tanggal setelah atau sama dengan :date.',
    'alpha'                => ':attribute hanya boleh berisi huruf.',
    'alpha_dash'           => ':attribute hanya boleh berisi huruf, angka, setrip, dan garis bawah.',
    'alpha_num'            => ':attribute hanya boleh berisi huruf dan angka.',
    'array'                => ':attribute harus berupa sebuah array.',
    'ascii'                => ':attribute hanya boleh berisi karakter alfanumerik byte tunggal dan simbol.',
    'before'               => ':attribute harus tanggal sebelum :date.',
    'before_or_equal'      => ':attribute harus tanggal sebelum atau sama dengan :date.',
    'between'              => [
        'array'   => ':attribute harus memiliki :min sampai :max anggota.',
        'file'    => ':attribute harus berukuran antara :min sampai :max kilobita.',
        'numeric' => ':attribute harus bernilai antara :min sampai :max.',
        'string'  => ':attribute harus berukuran antara :min sampai :max karakter.',
    ],
    'boolean'              => ':attribute harus bernilai true atau false',
    'can'                  => ':attribute mengandung nilai yang tidak sah.',
    'confirmed'            => 'Konfirmasi :attribute tidak cocok.',
    'current_password'     => 'Kata sandi salah.',
    'date'                 => ':attribute bukan tanggal yang valid.',
    'date_equals'          => ':attribute harus berupa tanggal yang sama dengan :date.',
    'date_format'          => ':attribute tidak cocok dengan format :format.',
    'decimal'              => ':attribute harus memiliki :decimal tempat desimal.',
    'declined'             => ':attribute harus ditolak.',
    'declined_if'          => ':attribute harus ditolak ketika :other bernilai :value.',
    'different'            => ':attribute dan :other harus berbeda.',
    'digits'               => ':attribute harus terdiri dari :digits angka.',
    'digits_between'       => ':attribute harus berukuran antara :min sampai :max angka.',
    'dimensions'           => ':attribute memiliki dimensi gambar yang tidak valid.',
    'distinct'             => ':attribute memiliki nilai yang duplikat.',
    'doesnt_end_with'      => ':attribute tidak boleh diakhiri dengan salah satu dari berikut: :values.',
    'doesnt_start_with'    => ':attribute tidak boleh diawali dengan salah satu dari berikut: :values.',
    'email'                => ':attribute harus berupa alamat surel yang valid.',
    'ends_with'            => ':attribute harus diakhiri salah satu dari berikut: :values',
    'enum'                 => ':attribute yang dipilih tidak valid.',
    'exists'               => ':attribute yang dipilih tidak valid.',
    'extensions'           => ':attribute harus memiliki salah satu ekstensi berikut: :values.',
    'file'                 => ':attribute harus berupa sebuah berkas.',
    'filled'               => ':attribute harus memiliki nilai.',
    'gt'                   => [
        'array'   => ':attribute harus memiliki lebih dari :value anggota.',
        'file'    => ':attribute harus berukuran lebih besar dari :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar dari :value.',
        'string'  => ':attribute harus berukuran lebih besar dari :value karakter.',
    ],
    'gte'                  => [
        'array'   => ':attribute harus mempunyai :value anggota atau lebih.',
        'file'    => ':attribute harus berukuran lebih besar dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai lebih besar dari atau sama dengan :value.',
        'string'  => ':attribute harus berukuran lebih besar dari atau sama dengan :value karakter.',
    ],
    'hex_color'            => ':attribute harus berupa warna heksadesimal yang valid.',
    'image'                => ':attribute harus berupa gambar.',
    'in'                   => ':attribute yang dipilih tidak valid.',
    'in_array'             => ':attribute tidak ada di dalam :other.',
    'integer'              => ':attribute harus berupa bilangan bulat.',
    'ip'                   => ':attribute harus berupa alamat IP yang valid.',
    'ipv4'                 => ':attribute harus berupa alamat IPv4 yang valid.',
    'ipv6'                 => ':attribute harus berupa alamat IPv6 yang valid.',
    'json'                 => ':attribute harus berupa string JSON yang valid.',
    'list'                 => ':attribute harus berupa daftar.',
    'lowercase'            => ':attribute harus berupa huruf kecil.',
    'lt'                   => [
        'array'   => ':attribute harus memiliki kurang dari :value anggota.',
        'file'    => ':attribute harus berukuran kurang dari :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari :value.',
        'string'  => ':attribute harus berukuran kurang dari :value karakter.',
    ],
    'lte'                  => [
        'array'   => ':attribute harus tidak lebih dari :value anggota.',
        'file'    => ':attribute harus berukuran kurang dari atau sama dengan :value kilobita.',
        'numeric' => ':attribute harus bernilai kurang dari atau sama dengan :value.',
        'string'  => ':attribute harus berukuran kurang dari atau sama dengan :value karakter.',
    ],
    'mac_address'          => ':attribute harus berupa alamat MAC yang valid.',
    'max'                  => [
        'array'   => ':attribute maksimal terdiri dari :max anggota.',
        'file'    => ':attribute maksimal berukuran :max kilobita.',
        'numeric' => ':attribute tidak boleh lebih besar dari :max.',
        'string'  => ':attribute tidak boleh lebih dari :max karakter.',
    ],
    'max_digits'           => ':attribute tidak boleh memiliki lebih dari :max digit.',
    'mimes'                => ':attribute harus berupa berkas berjenis: :values.',
    'mimetypes'            => ':attribute harus berupa berkas berjenis: :values.',
    'min'                  => [
        'array'   => ':attribute minimal terdiri dari :min anggota.',
        'file'    => ':attribute minimal berukuran :min kilobita.',
        'numeric' => ':attribute minimal bernilai :min.',
        'string'  => ':attribute minimal berisi :min karakter.',
    ],
    'min_digits'           => ':attribute tidak boleh memiliki kurang dari :min digit.',
    'missing'              => ':attribute harus tidak ada.',
    'missing_if'           => ':attribute harus tidak ada ketika :other adalah :value.',
    'missing_unless'       => ':attribute harus tidak ada kecuali :other adalah :value.',
    'missing_with'         => ':attribute harus tidak ada jika terdapat :values.',
    'missing_with_all'     => ':attribute harus tidak ada jika terdapat :values.',
    'multiple_of'          => ':attribute harus merupakan kelipatan dari :value.',
    'not_in'               => ':attribute yang dipilih tidak valid.',
    'not_regex'            => 'Format :attribute tidak valid.',
    'numeric'              => ':attribute harus berupa angka.',
    'password'             => [
        'letters'       => ':attribute harus mengandung setidaknya satu huruf.',
        'mixed'         => ':attribute harus mengandung setidaknya satu huruf besar dan satu huruf kecil.',
        'numbers'       => ':attribute harus mengandung setidaknya satu angka.',
        'symbols'       => ':attribute harus mengandung setidaknya satu simbol.',
        'uncompromised' => ':attribute yang diberikan telah muncul dalam kebocoran data. Silakan pilih :attribute yang berbeda.',
    ],
    'present'              => ':attribute wajib ada.',
    'present_if'           => ':attribute wajib ada ketika :other adalah :value.',
    'present_unless'       => ':attribute wajib ada kecuali :other adalah :value.',
    'present_with'         => ':attribute wajib ada jika terdapat :values.',
    'present_with_all'     => ':attribute wajib ada jika terdapat :values.',
    'prohibited'           => ':attribute dilarang.',
    'prohibited_if'        => ':attribute dilarang ketika :other adalah :value.',
    'prohibited_unless'    => ':attribute dilarang kecuali :other ada dalam :values.',
    'prohibits'            => ':attribute melarang :other untuk ada.',
    'regex'                => 'Format :attribute tidak valid.',
    'required'             => ':attribute wajib diisi.',
    'required_array_keys'  => ':attribute harus berisi entri untuk: :values.',
    'required_if'          => ':attribute wajib diisi bila :other adalah :value.',
    'required_if_accepted' => ':attribute wajib diisi bila :other diterima.',
    'required_unless'      => ':attribute wajib diisi kecuali :other memiliki nilai :values.',
    'required_with'        => ':attribute wajib diisi bila terdapat :values.',
    'required_with_all'    => ':attribute wajib diisi bila terdapat :values.',
    'required_without'     => ':attribute wajib diisi bila tidak terdapat :values.',
    'required_without_all' => ':attribute wajib diisi bila sama sekali tidak ada :values.',
    'same'                 => ':attribute dan :other harus sama.',
    'size'                 => [
        'array'   => ':attribute harus mengandung :size anggota.',
        'file'    => ':attribute harus berukuran :size kilobyte.',
        'numeric' => ':attribute harus berukuran :size.',
        'string'  => ':attribute harus berukuran :size karakter.',
    ],
    'starts_with'          => ':attribute harus diawali salah satu dari berikut: :values',
    'string'               => ':attribute harus berupa string.',
    'timezone'             => ':attribute harus berupa zona waktu yang valid.',
    'unique'               => ':attribute sudah terdaftar / sudah digunakan.',
    'uploaded'             => ':attribute gagal diunggah.',
    'uppercase'            => ':attribute harus berupa huruf besar.',
    'url'                  => ':attribute harus berupa URL yang valid.',
    'ulid'                 => ':attribute harus berupa ULID yang valid.',
    'uuid'                 => ':attribute harus berupa UUID yang valid.',

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Baris Bahasa Validasi
    |--------------------------------------------------------------------------
    |
    | Di sini Anda dapat menentukan pesan validasi kustom untuk atribut dengan
    | menggunakan konvensi "attribute.rule" untuk menamai baris.
    |
    */

    'custom' => [
        'email' => [
            'unique' => 'Email ini sudah terdaftar di sistem. Silakan gunakan email lain atau langsung masuk ke akun Anda.',
        ],
        'password' => [
            'confirmed' => 'Konfirmasi kata sandi tidak cocok.',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Kustomisasi Atribut Validasi
    |--------------------------------------------------------------------------
    |
    | Baris bahasa berikut digunakan untuk menukar 'placeholder' atribut kami
    | dengan sesuatu yang lebih ramah pengguna seperti "Alamat Email" daripada
    | "email". Ini membantu kita membuat pesan kita lebih ekspresif.
    |
    */

    'attributes' => [
        'name'                  => 'nama',
        'email'                 => 'email',
        'password'              => 'kata sandi',
        'password_confirmation' => 'konfirmasi kata sandi',
        'title'                 => 'judul',
        'description'           => 'deskripsi',
        'category'              => 'kategori',
        'priority'              => 'prioritas',
        'status'                => 'status',
    ],

];
