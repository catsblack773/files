<?php
return [
	'name' => 'files',
	'title' => 'Файловый менеджер',
	'settings' => [
		'path' => '/files/',
		'images' => [
			'isRename' => true,
			'thum_list' => [
				[
					'name' => 'small',
					'size' => 200,
					'resize' => true,
					'ratio' => true,
					'ratio_crop' => false
				],
				[
					'name' => 'middle',
					'size' => 450,
					'resize' => true,
					'ratio' => true,
					'ratio_crop' => false
				],
				[
					'name' => 'big',
					'size' => 900,
					'resize' => true,
					'ratio' => true,
					'ratio_crop' => false
				]
			]
		]
	]
];
