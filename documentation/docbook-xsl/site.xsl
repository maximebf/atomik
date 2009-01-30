<?xml version='1.0'?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">

	<xsl:import href="common.xsl"/>
	<xsl:import href="templates.xsl"/>
	
	<xsl:param name="html.stylesheet" select="concat($atomik.base, '/assets/css/main.css ', $atomik.stylesheet)"/>
	
	<xsl:template name="user.header.navigation">
		<xsl:call-template name="atomik.header" />
	</xsl:template>
	
	<xsl:template name="user.footer.navigation">
		<xsl:call-template name="atomik.footer" />
	</xsl:template>
	
	<xsl:template name="header.navigation">
		
	</xsl:template>

</xsl:stylesheet>
