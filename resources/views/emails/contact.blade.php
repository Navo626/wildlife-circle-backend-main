<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Contact Us</title>
    <style>
        *,
        ::before,
        ::after {
            box-sizing: border-box;
            border-width: 0;
            border-style: solid;
            border-color: #e5e7eb;
        }

        ::before,
        ::after {
            --tw-content: '';
        }

        html,
        :host {
            line-height: 1.5;
            -webkit-text-size-adjust: 100%;
            -moz-tab-size: 4;
            tab-size: 4;
            font-family: ui-sans-serif, system-ui, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            font-feature-settings: normal;
            font-variation-settings: normal;
            -webkit-tap-highlight-color: transparent;
        }

        body {
            margin: 0;
            line-height: inherit;
        }

        hr {
            height: 0;
            color: inherit;
            border-top-width: 1px;
        }

        abbr:where([title]) {
            text-decoration: underline dotted;
        }

        h1, h2, h3, h4, h5, h6 {
            font-size: inherit;
            font-weight: inherit;
        }

        a {
            color: inherit;
            text-decoration: inherit;
        }

        b, strong {
            font-weight: bolder;
        }

        code, kbd, samp, pre {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
            font-feature-settings: normal;
            font-variation-settings: normal;
            font-size: 1em;
        }

        small {
            font-size: 80%;
        }

        sub, sup {
            font-size: 75%;
            line-height: 0;
            position: relative;
            vertical-align: baseline;
        }

        sub {
            bottom: -0.25em;
        }

        sup {
            top: -0.5em;
        }

        table {
            text-indent: 0;
            border-color: inherit;
            border-collapse: collapse;
        }

        button, input, optgroup, select, textarea {
            font-family: inherit;
            font-size: 100%;
            font-weight: inherit;
            line-height: inherit;
            color: inherit;
            margin: 0;
            padding: 0;
        }

        button, select {
            text-transform: none;
        }

        button, input:where([type='button']), input:where([type='reset']), input:where([type='submit']) {
            -webkit-appearance: button;
            background-color: transparent;
            background-image: none;
        }

        :-moz-focusring {
            outline: auto;
        }

        :-moz-ui-invalid {
            box-shadow: none;
        }

        progress {
            vertical-align: baseline;
        }

        ::-webkit-inner-spin-button, ::-webkit-outer-spin-button {
            height: auto;
        }

        [type='search'] {
            -webkit-appearance: textfield;
            outline-offset: -2px;
        }

        ::-webkit-search-decoration {
            -webkit-appearance: none;
        }

        ::-webkit-file-upload-button {
            -webkit-appearance: button;
            font: inherit;
        }

        summary {
            display: list-item;
        }

        blockquote, dl, dd, h1, h2, h3, h4, h5, h6, hr, figure, p, pre {
            margin: 0;
        }

        fieldset {
            margin: 0;
            padding: 0;
        }

        legend {
            padding: 0;
        }

        ol, ul, menu {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        dialog {
            padding: 0;
        }

        textarea {
            resize: vertical;
        }

        input::placeholder, textarea::placeholder {
            opacity: 1;
            color: #9ca3af;
        }

        button, [role="button"] {
            cursor: pointer;
        }

        :disabled {
            cursor: default;
        }

        img, svg, video, canvas, audio, iframe, embed, object {
            display: block;
            vertical-align: middle;
        }

        img, video {
            max-width: 100%;
            height: auto;
        }

        [hidden] {
            display: none;
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .mt-2 {
            margin-top: 0.5rem;
        }

        .mt-3 {
            margin-top: 0.75rem;
        }

        .mt-8 {
            margin-top: 2rem;
        }

        .flex {
            display: flex;
        }

        .h-12 {
            height: 3rem;
        }

        .w-auto {
            width: auto;
        }

        .max-w-2xl {
            max-width: 42rem;
        }

        .items-center {
            align-items: center;
        }

        .justify-center {
            justify-content: center;
        }

        .rounded-2xl {
            border-radius: 1rem;
        }

        .border-t-2 {
            border-top-width: 2px;
        }

        .px-6 {
            padding-left: 1.5rem;
            padding-right: 1.5rem;
        }

        .py-8 {
            padding-top: 2rem;
            padding-bottom: 2rem;
        }

        .font-semibold {
            font-weight: 600;
        }

        .leading-loose {
            line-height: 2;
        }

        .text-gray-500 {
            --tw-text-opacity: 1;
            color: rgb(107 114 128 / var(--tw-text-opacity));
        }

        .text-gray-600 {
            --tw-text-opacity: 1;
            color: rgb(75 85 99 / var(--tw-text-opacity));
        }

        .text-gray-700 {
            --tw-text-opacity: 1;
            color: rgb(55 65 81 / var(--tw-text-opacity));
        }
    </style>
</head>
<body>
    <section class="section max-w-2xl px-6 py-8 mx-auto rounded-2xl">
        <header class="flex justify-center items-center">
            @php
                $path = public_path('assets/logo.png');
                $type = pathinfo($path, PATHINFO_EXTENSION);
                $data = file_get_contents($path);
                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
            @endphp
            <img class="w-auto h-12" src="{{ $base64 }}" alt="">
        </header>

        <main class="mt-8">
            <h2 class="text-gray-700">Hi admin,</h2>
            <h2 class="mt-2 text-gray-700">You have received a new feedback.</h2>

            <p class="mt-8 leading-loose text-gray-600">
                <span class="font-semibold">Name: </span>{{ $firstname }} {{ $lastname }}
            </p>
            <p class="mt-2 leading-loose text-gray-600">
                <span class="font-semibold">Phone Number: </span>{{ $phone }}
            </p>
            <p class="mt-2 leading-loose text-gray-600">
                <span class="font-semibold">Email: </span>{{ $email }}
            </p>
            <p class="mt-2 leading-loose text-gray-600">
                <span class="font-semibold">Message: </span>{{ $details }}
            </p>
        </main>

        <footer class="mt-8 flex justify-center items-center border-t-2">
            <p class="mt-3 text-gray-500">&copy; 2024 Wildlife Circle. All Rights Reserved.</p>
        </footer>
    </section>
</body>
</html>
