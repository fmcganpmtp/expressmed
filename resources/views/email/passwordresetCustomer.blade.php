<h2>{{ $subject }}</h2><br />

<h4>Dear {{ $name }},</h4> <br />

<p>We have sent you this email in response to your request to reset your password on Expressmed customer profile. After you reset your password, you can login your account with new password.</p><br />

Click the following links and proceed the instruction

<br />

<br />

<a href="{{ url('user/reset/verify/' . $email . '/' . $token) }}" style="display: inline-block;color: #fff;text-decoration: none;text-transform: uppercase;font-weight: bold;background: #3067f0;padding: 12px 26px 12px 26px;border-radius: 8px;">Reset Password</a>

<br />

<br />

Regards,

<br />
Expressmed Team
<br />

www.Expressmed.in
