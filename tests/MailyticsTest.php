<?php

// <a href='https://google.com">Respond To Inquiry></a>
it('can sanitize urls using single quote within href anchor tags');
// <a href="https://google.com">Respond To Inquiry></a>
it('can sanitize urls using double quote within href anchor tags');
// <a href="{{$url}}">Respond To Inquiry></a>
it('can sanitize urls using variable within href anchor tags');
// <x-mail::button :url='https://stackoverflow.com'>View</x-mail::button>
it('can sanitize urls using single quotes within :url blade component');
// <x-mail::button :url="https://stackoverflow.com">View</x-mail::button>
it('can sanitize urls using double quotes within :url blade component');
// <x-mail::button :url="{{$url}}">View</x-mail::button>
it('can sanitize urls using variable within :url blade component');
// check if file exists
it('can generate file for image pixel');
it('can compile a mailable to a generated view');
it('can track when an email has been viewed');
it('can track when an email has been clicked');
