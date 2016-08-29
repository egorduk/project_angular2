import { Component, ElementRef, EventEmitter, Directive, Input, Output } from '@angular/core';

@Directive({
    selector: '[check-picture]'
})

export class CheckGalleryDirective {

    //private _pictures: string;

    /* @Input()('check-gallery')
     set checkGallery(value: string) {
     this._pictures = value;
     }*/

    @Input()
    data: boolean = false;

    @Output()
    response: EventEmitter<boolean> = new EventEmitter<boolean>();

    constructor(private element: ElementRef) {
        console.log('init');
    }

    private ngOnInit() {
        console.log('ngOnInit', this.data);
    }

    protected ngOnChanges() {
        console.log('ngOnChanges', this.data);
        let data = this.data;
        let response = this.response;
        response.emit({
            value: false
        });


    }

    private ngOnDestroy() {
        console.log('ngOnDestroy', this.data);
    }
}