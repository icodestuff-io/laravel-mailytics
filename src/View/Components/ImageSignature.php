<?php

namespace Icodestuff\Mailytics\View\Components;

use Illuminate\View\Component;

class ImageSignature extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(public string $url)
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return view('components.image-signature');
    }
}
