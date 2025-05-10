<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td class="content-cell" align="center">
{!!trans('email.footer', [
    'copyright' => '&copy;',
    'year'      => now()->year,
    'app'       => '<a href="'.config('app.url').'" target="_blank">'.config('app.name').'</a>'
]) !!}
</td>
</tr>
</table>
</td>
</tr>
