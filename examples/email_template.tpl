<html>

<head>

    <title><?= $YD_FW_NAMEVERS ?></title>

    <style>
        body { 
            font-family: "Georgia", "Times New Roman", "Times";
            margin: 45px;
            background-color: lightyellow;
        };
        p {
            font-size: 85%;
            line-height: 150%;
        }
        a {
            color: red;
        }
        .title1 {
            font-size: 125%;
            color: darkgreen;
            font-weight: normal;
            letter-spacing: 3px;
        }
        .title2 {
            font-size: 100%;
            color: darkgreen;
            font-weight: normal;
            letter-spacing: 3px;
            padding-top: 10px;
            font-style: italic;

        }
    </style>

</head>

<body>

    <p class="title1">Hello from Pieter & Fiona</p>

    <p><img src="fsimage1.jpg" border="1" alt="Pieter & Fiona"
    width="320" height="213"></p>

    <p>This email was sent to <a href="mailto:<?= $email ?>"><?= $email ?></a>.</p>

    <p class="title2">Lorem Ipsum</p>

    <p>Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Ut feugiat
    volutpat turpis. Maecenas auctor luctus quam. Donec ac orci quis tortor
    congue venenatis. Morbi hendrerit, dolor eget posuere rhoncus, nulla libero
    viverra mi, vitae convallis mi tortor et leo. Integer eget purus. Mauris
    vitae enim id tortor ullamcorper pulvinar. Aliquam vel massa in est
    malesuada tristique. Aliquam turpis. Morbi pretium, velit nec porttitor
    fringilla, nibh ipsum pellentesque metus, quis sollicitudin felis lectus vel
    elit. Nulla facilisi. Etiam nec augue in ipsum tristique condimentum. Nullam
    egestas lorem at lacus. Mauris mollis, eros ultrices auctor condimentum,
    nulla elit pharetra ipsum, ac ullamcorper justo odio quis mauris. Maecenas
    id nulla. Nunc pellentesque augue ac pede. Aliquam erat volutpat.</p>

    <p class="title2"><?= $YD_FW_NAMEVERS ?></p>

    <p>
        Thanks for trying out
        <a href="<?= $YD_FW_HOMEPAGE ?>"><?= $YD_FW_NAMEVERS ?></a>.
    </p>

    <p class="title2">Oh, yes, important!</p>

    <p>If everything went fine, you should also find an attachment of a file
    in this email.</p>

</body>

</html>
