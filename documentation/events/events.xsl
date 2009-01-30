<?xml version='1.0'?>
<xsl:stylesheet version="1.0" 
	xmlns:xsl="http://www.w3.org/1999/XSL/Transform" 
	xmlns:exsl="http://exslt.org/common"
	xmlns:str="http://exslt.org/strings"
	extension-element-prefixes="exsl str">

	<xsl:import href="../docbook-xsl/templates.xsl"/>
	
	<xsl:template match="/events">
		<xsl:call-template name="file.write">
			<xsl:with-param name="filename" select="'index.html'" />
			<xsl:with-param name="content">
				<p>
					This tool allows you to browse available events from the core and official plugins.
				</p>
			</xsl:with-param>
		</xsl:call-template>
		<xsl:for-each select="category">
			<xsl:variable name="cat" select="@name" />
			<xsl:for-each select="group">
				<xsl:call-template name="file.write">
					<xsl:with-param name="filename" select="concat($cat, '-', @name, '.html')" />
					<xsl:with-param name="content"><xsl:apply-templates select="." /></xsl:with-param>
				</xsl:call-template>
			</xsl:for-each>
		</xsl:for-each>
	</xsl:template>
	
	<xsl:template name="file.write">
		<xsl:param name="filename" />
		<xsl:param name="content" />
		
		<xsl:message>Creating file: <xsl:value-of select="$filename" /></xsl:message>
		<exsl:document href="{$filename}" method="html" doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" encoding="utf-8" version="1.0">
			<html>
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
					
					<title>Atomik Framework Event Browser</title>
					<link rel="stylesheet" type="text/css" href="{$atomik.base}/assets/css/main.css" />
					<link rel="stylesheet" type="text/css" href="events.css" />
				</head>
				<body>
					<xsl:call-template name="atomik.header" />
					<div id="main-wrapper">
						<div id="main">
							<h1>Atomik Event Browser</h1>
							<div id="sidebar">
								<xsl:call-template name="sidebar" />
							</div>
							<div id="content">
								<xsl:copy-of select="$content" />
							</div>
							<div class="clear" />
						</div>
					</div>
					<xsl:call-template name="atomik.footer" />
				</body>
			</html>
		</exsl:document>
	</xsl:template>
	
	<xsl:template name="sidebar">
		<ul class="categories">
			<xsl:for-each select="/events/category">
				<li>
					<xsl:variable name="cat" select="@name" />
					<span class="category-title"><xsl:value-of select="@title" /></span>
					<ul class="groups">
						<xsl:for-each select="group">
							<li>
								<a class="group-title">
									<xsl:attribute name="href"><xsl:value-of select="concat($cat, '-', @name, '.html')" /></xsl:attribute>
									<xsl:value-of select="@title" />
								</a>
							</li>
						</xsl:for-each>
					</ul>
				</li>
			</xsl:for-each>
		</ul>
	</xsl:template>
	
	<xsl:template match="group">
		<div class="group">
			<h2 class="group-title"><xsl:value-of select="@title" /></h2>
			<div class="events">
				<xsl:apply-templates />
			</div>
		</div>
	</xsl:template>
	
	<xsl:template match="event">
		<div class="event">
			<h3 class="event-name"><xsl:value-of select="@name" /></h3>
			<p class="event-description">
				<xsl:value-of select="description" />
			</p>
			<div class="event-params">
				<ol>
					<xsl:for-each select="param | refparam">
						<li class="event-param">
							<xsl:if test="name() = 'refparam'">
								<xsl:attribute name="class">event-param event-refparam</xsl:attribute>
							</xsl:if>
							<span class="event-param-type"><xsl:value-of select="@type" />&#160;</span>
							<span class="event-param-name">
								<xsl:if test="name() = 'refparam'"><span class="event-param-ref">&amp;</span></xsl:if>
								<xsl:value-of select="@name" />
							</span>
							<p class="event-param-description">
								<xsl:value-of select="." />
							</p>
						</li>	
					</xsl:for-each>
				</ol>
			</div>
			<div class="event-example">
				<code>
					<xsl:variable name="func" select="concat('my', str:replace(@name, '::', ''), 'Handler')" />
					<xsl:variable name="args">
						<xsl:for-each select="param | refparam">
							<xsl:if test="name() = 'refparam'">&amp;</xsl:if>
							<xsl:value-of select="@name" />
							<xsl:if test="position() &lt; last()">,&#160;</xsl:if>
						</xsl:for-each>
					</xsl:variable>
					function <xsl:value-of select="$func" />(<xsl:value-of select="$args" />)<br />
					{<br />
					&#160;&#160;&#160;&#160;// your code<br />
					}<br />
					<br />
					Atomik::listenEvent('<xsl:value-of select="@name" />', '<xsl:value-of select="$func" />');
				</code>
			</div>
		</div>
	</xsl:template>

</xsl:stylesheet>
