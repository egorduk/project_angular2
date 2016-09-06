import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { routing } from './app.routes';

import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';

import { DataService } from './common/service/data.service';
import { SafeBgPipe } from './common/pipe/safe.pipe';
import { AppComponent }  from './app.component';
import { AlertComponent } from 'ng2-bootstrap/components/alert';
import { DropdownModule  } from 'ng2-bootstrap/components/dropdown';
import { SELECT_DIRECTIVES } from 'ng2-select/ng2-select';
import { ModalModule } from "ng2-modal";
import { LoginComponent } from './login/login.component';
//import { GalleryComponent } from './gallery/gallery.component';
import { FileSizePipe } from './common/pipe/fileSize.pipe';
import { SafeFileExtPipe } from './common/pipe/safe.pipe';
import { FILE_UPLOAD_DIRECTIVES } from 'ng2-file-upload/ng2-file-upload';
import { TabsModule } from 'ng2-bootstrap/components/tabs';

@NgModule({
    imports: [ BrowserModule, FormsModule, HttpModule, routing, DropdownModule, ModalModule, TabsModule ],       // module dependencies
    declarations: [ AppComponent,
        AlertComponent,
        FileSizePipe,
        SafeFileExtPipe,
        SafeBgPipe,
        FILE_UPLOAD_DIRECTIVES,
        SELECT_DIRECTIVES,
        LoginComponent ],   // components and directives
    bootstrap: [ AppComponent ],     // root component
    providers: [ AUTH_PROVIDERS, DataService ]                    // services
})
export class AppModule { }