<html>
<h1>Você recebeu uma transferência!</h1>
<p>De: {{ $payer->name }} Para: {{$payee->name}}</p>
<p>Valor: R$ {{ number_format($value, 2, ',', '.') }}</p>
</html>
