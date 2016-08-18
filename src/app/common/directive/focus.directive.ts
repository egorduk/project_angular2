import { Component, ElementRef, Directive, Input } from '@angular/core';

@Directive({
    selector: '[focus]'
})

export class FocusDirective {
@Input()
    focus: boolean = false;

    constructor(private element: ElementRef) {
    }

    protected ngOnChanges() {
        if (this.focus) {
            this.element.nativeElement.focus();
        }
    }
}