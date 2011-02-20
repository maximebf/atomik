<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="common.xsl"/>
	<xsl:import href="templates.xsl"/>
	
	<xsl:param name="syntaxhighlighter.css.core" select="'http://alexgorbatchev.com/pub/sh/2.1.364/styles/shCore.css'" />
	<xsl:param name="syntaxhighlighter.css.default" select="'http://alexgorbatchev.com/pub/sh/2.1.364/styles/shThemeDefault.css'" />
	<xsl:param name="html.stylesheet" select="concat($atomik.base, '/assets/css/main.css ', $atomik.stylesheet, ' ', $syntaxhighlighter.css.core, ' ', $syntaxhighlighter.css.default)"/>
	
	<xsl:template name="user.header.navigation">
		<xsl:call-template name="atomik.header" />
	</xsl:template>
	
	<xsl:template name="user.footer.navigation">
		<xsl:call-template name="atomik.footer" />
	</xsl:template>
	
	<xsl:template name="header.navigation">
		
	</xsl:template>
	
	<xsl:template match="programlisting" mode="class.value">
		<xsl:value-of select="'brush: php; programlisting'"/>
	</xsl:template>

</xsl:stylesheet>
