
{{ content() }}

<div align="center" class="well">

	{{ form('class': 'form-search') }}

	<div align="left">
		<h2>Transferencia de Fondos</h2>
	</div>

		{{ form.render('userId') }}
        {{ form.render('amount') }}
		{{ form.render('go') }}

		{{ form.render('csrf', ['value': security.getToken()]) }}

		<hr>
	</form>

</div>

{% if request.isPost() and txResult %}
<h2>Resultado de Transferencia</h2>

<div class="well" align="center">

	<table class="perms">
		<tr>
			{{ txResult }}
		</tr>
	</table>

</div>
{% endif %}