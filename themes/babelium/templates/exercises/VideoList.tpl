
		<section class="exerciseList VBox">
			<header>
				<h1>Practice exercise list</h1>
			</header>
			
{include file="util/Pagination.tpl"}
			
			<div class="exerciseContainer">
{foreach from=$exercises item=exercise name=videos}
	{assign var="iteration" value=$smarty.foreach.videos.iteration}
	{include file="exercises/ExerciseItemRender.tpl"}
{/foreach}
			</div>
			
{include file="util/Pagination.tpl"}

		</section>