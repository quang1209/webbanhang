<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content=
		"width=device-width, initial-scale=1.0">

	<title>Coupon Example</title>

	<style>
		body {
			margin: 0;
			padding: 0;
			background-color: #f0f0f0;
			display: flex;
			justify-content: center;
			align-items: center;
			height: 100vh;
		}

		.coupon {
			background-color: #fff;
			box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
			border-radius: 5px;
			padding: 20px;
			max-width: 300px;
			text-align: center;
		}

		.coupon img {
			max-width: 100%;
			height: auto;
			border-radius: 5px;
		}

		.coupon h2 {
			color: #35108d;
			margin-top: 10px;
		}

		.coupon p {
			color: #777;
			line-height: 1.5;
			margin-top: 10px;
		}

		.coupon .code {
			font-size: 24px;
			color: #35108d;
			margin-bottom: 10px;
		}

		.coupon .expiration {
			color: #e80c0c;
			font-size: 14px;
		}
	</style>
</head>

<body>
	<div class="coupon">
		<div class="logo">
			<img src= {{  asset('images/coupon.png') }}
				alt="Coupon Logo">
		</div>

		<h2>Mã giảm giá mới</h2>
		<p>
			Lấy <span class="code">{{ $coupon['d_code'] }}</span>
			cho đơn hàng tiếp theo của bạn
		</p>

		<p>
			Sử dụng code ở giỏ hàng
		</p>

		<p>Hết hạn: <span class="expiration">
			{{ $coupon['d_date_end'] }}</span>
		</p>
	</div>
</body>

</html>
