import { NgModule }      from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { FormsModule } from '@angular/forms';
import { HttpModule } from '@angular/http';

import { routing } from './app.routes';

import { AUTH_PROVIDERS } from 'angular2-jwt/angular2-jwt';
//import { AuthHttp } from 'angular2-jwt/angular2-jwt';

import { DataService } from './common/service/data.service';
import { SafeBgPipe, SafeAvatarPipe, SafeFileExtPipe } from './common/pipe/safe.pipe';
import { AppComponent }  from './app.component';
import { AlertComponent } from 'ng2-bootstrap/components/alert';
import { DropdownModule  } from 'ng2-bootstrap/components/dropdown';
import { SELECT_DIRECTIVES } from 'ng2-select/ng2-select';
import { ModalModule } from "ng2-modal";
import { FileSizePipe } from './common/pipe/fileSize.pipe';
import { FILE_UPLOAD_DIRECTIVES } from 'ng2-file-upload/ng2-file-upload';
import { TabsModule } from 'ng2-bootstrap/components/tabs';
import { AuthGuard } from './common/auth.guard';
import { FocusDirective } from './common/directive/focus.directive';
import { MessageService } from './common/service/message.service';
import { LoggedService } from './common/service/logged.service';
import { GlobalService } from './common/service/global.service';
import { HeaderComponent } from './header/header.component';

import { Md5 } from 'ts-md5/dist/md5';

@NgModule({
    imports: [ BrowserModule, FormsModule, HttpModule, routing, DropdownModule, ModalModule, TabsModule ],       // module dependencies
    declarations: [ AppComponent,
        AlertComponent,
        FileSizePipe,
        SafeFileExtPipe,
        SafeBgPipe,
        SafeAvatarPipe,
        FILE_UPLOAD_DIRECTIVES,
        SELECT_DIRECTIVES,
        FocusDirective,
        HeaderComponent
    ],   // components and directives
    bootstrap: [ AppComponent ],     // root component
    providers: [ AUTH_PROVIDERS, DataService, Md5, AuthGuard, MessageService, LoggedService, GlobalService ]                    // services
})
export class AppModule { }