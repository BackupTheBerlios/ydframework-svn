<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">

        <html>

        <head>

            <meta http-equiv="Content-Type" content="text/html;charset=iso-8859-1" />
            <title>YDF2 - Build history</title>
            <link href="api/doxygen.css" rel="stylesheet" type="text/css" />
            <style>
                td { border-bottom: 1px solid #CCCCCC; }
                table { border: 0px; width: 80%; }
            </style>

        </head>

        <body>

            <h1>Yellow Duck Framework </h1>

            <h3 align="center">version 2.0.0 </h3>

            <hr size="1" noshade="" />

            <h3>Build history</h3>

            <xsl:for-each select="log/logentry">
                <hr size="1" noshade="" />
                <p><b>Build <xsl:value-of select="@revision" /></b></p>
                <p><xsl:value-of select="msg" /></p>
                <blockquote><table cellspacing="0" cellpadding="3">
                    <tr>
                        <td colspan="2"><b>Affected files</b></td>
                    </tr>
                    <xsl:for-each select="paths/path">
                        <tr>
                            <td><xsl:value-of select="text()" /></td>
                            <td width="10%" align="right"><xsl:choose>
                                    <xsl:when test="@action = 'A'">add</xsl:when>
                                    <xsl:when test="@action = 'M'">modify</xsl:when>
                                    <xsl:when test="@action = 'D'">delete</xsl:when>
                                    <xsl:otherwise>unknown</xsl:otherwise>
                            </xsl:choose></td>
                        </tr>
                    </xsl:for-each>
                </table></blockquote>
            </xsl:for-each>

            <hr size="1" noshade="" />

            <address><small>
                Yellow Duck Framework 2.0.0 by Pieter Claerhout,
                <a href="mailto:pieter@yellowduck.be">pieter@yellowduck.be</a>
            </small></address>

        </body>

        </html>

    </xsl:template>

</xsl:stylesheet>