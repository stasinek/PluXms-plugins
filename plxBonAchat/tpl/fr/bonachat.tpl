<page backimg="{{ backgroundimg_recto }}" backtop="7mm" backbottom="7mm" backleft="7mm" backright="10mm">
</page>
<page backimg="{{ backgroundimg_verso }}" backtop="20mm" backbottom="7mm" backleft="20mm" backright="10mm">
	<p style="text-align: right;"><span style="color: #{{ text_color }}">N° {{ idcode }}</span></p>
	<p><span style="color: #{{ text_color }};">{{ title }} {{ name }} {{ firstname }}</span></p>
	<p><span style="color: #{{ text_color }};">a le plaisir d'offrir ce bon d'achat d'une valeur de {{ price }}.</span></p>
	<p><span style="color: #{{ text_color }};">à l'attention de {{ recipient_title }} {{ recipient_name }} {{ recipient_firstname }}.</span></p>
	<p><span style="color: #{{ text_color }};">prestation valable jusqu'au {{ expiration_date }}.</span></p>
	<p style="text-align: right;"><span style="color: #{{ text_color }}">Ni repris, ni remboursé</span></p>
</page>