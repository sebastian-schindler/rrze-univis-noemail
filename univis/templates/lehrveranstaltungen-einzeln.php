<?php if ($daten['veranstaltung']) :
    _rrze_debug($daten['veranstaltung']['name']);
    foreach ($daten['veranstaltung'] as $veranstaltung) : ?>
	<h2><?php echo $veranstaltung['name'];?></h2>
        <?php 
        
        
        ?>
    <?php endforeach;
                
endif; ?>

{{#veranstaltung}}
	<h2>{{name}}</h2>

	{{#dozs}}
		<h3>Dozent/in</h3>
		{{#doz}}
			<h6><a href="http://univis.uni-erlangen.de/prg?search=persons&id={{ id }}&show=info">{{title}} {{firstname}} {{lastname}}</a></h6>
		{{/doz}}
	{{/dozs}}

	<h3>Angaben</h3>

	<p>
	{{{angaben}}}<br>
	</p>

	<h4>Zeit und Ort:</h4>
	<ul>
	{{#terms}}
		{{#term}}
			<li>{{date}} {{starttime}}-{{endtime}} Uhr, {{room_short}}{{#exclude}} (außer {{exclude}}){{/exclude}}</li>
		{{/term}}
		<br/>
	{{/terms}}
	</ul>

	<h4>Studienf&auml;cher / Studienrichtungen</h4>
	<p>
	{{#studs}}
		{{#stud}}
			{{pflicht}} {{richt}} {{sem}} (ECTS-Credits: {{credits}})<br>
		{{/stud}}
	{{/studs}}
	</p>

	{{#organizational}}
	<h4>Voraussetzungen / Organisatorisches</h4>
	<p>
	{{{organizational}}}
	</p>
	{{/organizational}}

	{{#summary}}
	<h4>Inhalt</h4>
	<p>
	{{{summary}}}
	</p>
	{{/summary}}


	{{#ects_infos}}
	<h4>ECTS-Informationen</h4>
		
	{{#ects_name}}
		<h5>Title:</h5>
		<p>{{ects_name}}</p>
	{{/ects_name}}

	{{#ects_content}}
		<h5>Content:</h5>
		<p>{{{ects_summary}}}</p>
	{{/ects_content}}

	{{#ects_literature}}
		<h5>Literature:</h5>
		<p>{{ects_literature}}</p>
	{{/ects_literature}}
	{{/ects_infos}}

	{{#zusatzinfos}}
	<h4>Zus&auml;tzliche Informationen</h4>
	<p>
		{{#keywords}}
			Schlagw&ouml;rter: {{keywords}} <br/>
		{{/keywords}}

		{{#turnout}}
			Erwartete Teilnehmerzahl: {{turnout}} <br/>
		{{/turnout}}


		{{#url_description}}
			www: <a href="{{url_description}}">{{url_description}}</a> <br/>
		{{/url_description}}
	</p>
	{{/zusatzinfos}}
{{/veranstaltung}}

{{#assets}}
	{{#download_link}}
		<a href="{{download_link}}"> Download </a>
	{{/download_link}}
{{/assets}}