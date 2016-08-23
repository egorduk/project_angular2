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
    picture: Array[];

    @Input()
    gallery: Array[];

    @Output()
    response: EventEmitter<boolean> = new EventEmitter<boolean>();

    constructor(private element: ElementRef) {
    }

    protected ngOnChanges() {
    //console.log('Gallery', this.gallery);
    //console.log('Picture', this.picture);
    let gallery = this.gallery;
    let response = this.response;
    let picture = this.picture;

    /*if (picture.gallery_ids) {
     picture.gallery_ids.forEach(
     function(val, index) {

     response.emit({
     value: false
     });

     console.log(val);
     console.log('Gallery id = ', gallery.gallery_id);

     if (val == gallery.gallery_id) {
     response.emit({
     value: true
     });

     return;
     } *//*else {
     response.emit({
     value: false
     });
     }*//*
     }
     );
     console.log('---------------');
     }*/

    if (typeof gallery.picture_ids === 'string') {
        gallery.picture_ids = gallery.picture_ids.split(',');
        console.log(gallery.picture_ids);

        gallery.picture_ids.forEach(
            function(val, index) {

                response.emit({
                    value: false
                });

                if (val == picture.picture_id) {
                    response.emit({
                        value: true
                    });
                }
            })
    }
}
}