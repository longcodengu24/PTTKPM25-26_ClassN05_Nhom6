@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Nạp coin (Test)</h2>
    <p>Ấn nút bên dưới để giả lập giao dịch thành công.</p>
    <button id="btnTest" class="btn btn-success">Test: Giao dịch thành công</button>

    <pre id="result" class="mt-3"></pre>
</div>

<script>
document.getElementById('btnTest').addEventListener('click', async () => {
    const res = await fetch('/test-transaction');
    const data = await res.json();
    document.getElementById('result').textContent = JSON.stringify(data, null, 2);
});
</script>
@endsection
