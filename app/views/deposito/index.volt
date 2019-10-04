
{{ content() }}

<div align="center" class="well">

	{{ form('class': 'form-search') }}

	<div align="left">
		<h2>Depósito</h2>
	</div>
        {{ form.render('amount') }}
		{{ form.render('go') }}

		{{ form.render('csrf', ['value': security.getToken()]) }}

		<hr>
	</form>

</div>

{% if request.isPost() and txResult %}
<h2>Resultado de la Transferencia</h2>

<div class="well" align="center">

	<table class="perms">
		<tr>
			{{ txResult }}
		</tr>
	</table>

</div>
{% endif %}