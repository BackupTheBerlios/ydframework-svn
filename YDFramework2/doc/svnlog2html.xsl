<?xml version="1.0" encoding="ISO-8859-1"?>

<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform">

    <xsl:template match="/">

        <html>

        <head>
            <title>YDF2 - Change log</title>
        </head>

        <body>

            <h1>Yellow Duck Framework version 2.0.0</h1>

            <xsl:for-each select="log/logentry">
                <hr size="1" noshade="" />
                <h3>
                    Build <xsl:value-of select="@revision" />
                    by <xsl:value-of select="author" />
                </h3>
                <p><code>
                    <xsl:value-of select="msg" />
                </code></p>
                <p><b>Affected Files</b></p>
                <ul>
                <xsl:for-each select="paths/path">
                    <li>[
                        <xsl:choose>
                            <xsl:when test="@action = 'A'">add</xsl:when>
                            <xsl:when test="@action = 'M'">modify</xsl:when>
                            <xsl:when test="@action = 'D'">delete</xsl:when>
                            <xsl:otherwise>unknown</xsl:otherwise>
                        </xsl:choose>
                    ] <xsl:value-of select="text()" /></li>
                </xsl:for-each>
                </ul>
            </xsl:for-each>

        </body>

        </html>

    </xsl:template>

</xsl:stylesheet>