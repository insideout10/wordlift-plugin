"use strict";(self.webpackChunkwordlift_plugin_app=self.webpackChunkwordlift_plugin_app||[]).push([[605],{5605:(p,g,n)=>{n.r(g),n.d(g,{AdminModule:()=>C});var r=n(6895),s=n(8176),t=n(4650),u=n(529),c=n(4963),a=n(3325),i=n(9562),z=n(7423),e=n(8284);function f(o,l){if(1&o){const d=t.EpF();t.ynx(0),t.TgZ(1,"a",4),t._UZ(2,"span",5),t._uU(3," Dev Menu "),t._UZ(4,"span",6),t.qZA(),t.TgZ(5,"nz-dropdown-menu",null,7)(7,"ul",8)(8,"li",9),t.NdJ("click",function(){t.CHM(d);const h=t.oxw();return t.KtG(h._setSynchronizationNotStarted())}),t._uU(9," Scenario: Synchronization Not Started (default) "),t.qZA(),t.TgZ(10,"li",9),t.NdJ("click",function(){t.CHM(d);const h=t.oxw();return t.KtG(h._setSynchronizationStarted())}),t._uU(11," Scenario: Synchronization Started "),t.qZA()()(),t.BQk()}if(2&o){const d=t.MAs(6);t.xp6(1),t.Q6J("nzDropdownMenu",d)}}const M=[{path:"",component:(()=>{class o{constructor(d){this.http=d,this.title="WordLift Plugin",this.devMode=(0,t.X6Q)()}_setSynchronizationNotStarted(){this.http.get("/api/scenarios/synchronization-not-started").subscribe()}_setSynchronizationStarted(){this.http.get("/api/scenarios/synchronization-started").subscribe()}}return o.\u0275fac=function(d){return new(d||o)(t.Y36(u.eN))},o.\u0275cmp=t.Xpm({type:o,selectors:[["wlx-admin"]],decls:9,vars:2,consts:[["src","assets/wordlift-logo.svg","routerLink","./dashboard","width","114","height","36","alt","WordLift logo",1,"logo"],[1,"separate"],[3,"nzAutoGenerate"],[4,"ngIf"],["id","dev-menu","nz-dropdown","",3,"nzDropdownMenu"],["nz-icon","","nzType","bug","nzTheme","outline"],["nz-icon","","nzType","down"],["menu","nzDropdownMenu"],["nz-menu","","nzSelectable",""],["nz-menu-item","",3,"click"]],template:function(d,m){1&d&&(t.TgZ(0,"nz-layout")(1,"nz-header"),t._UZ(2,"img",0),t.TgZ(3,"span",1),t._uU(4,"/"),t.qZA(),t._UZ(5,"nz-breadcrumb",2),t.YNc(6,f,12,1,"ng-container",3),t.qZA(),t.TgZ(7,"nz-content"),t._UZ(8,"router-outlet"),t.qZA()()),2&d&&(t.xp6(5),t.Q6J("nzAutoGenerate",!0),t.xp6(1),t.Q6J("ngIf",m.devMode))},dependencies:[r.O5,s.lC,s.rH,c.Dg,a.wO,a.r9,i.cm,i.Ws,i.RR,z.Ls,e.hw,e.E8,e.OK],styles:[".logo[_ngcontent-%COMP%]{cursor:pointer}.ant-layout-header[_ngcontent-%COMP%]{display:flex;flex-direction:row;align-items:center;gap:8px;color:#000000e0;font-size:14px;font-style:normal;font-weight:400;line-height:24px}.ant-layout-header[_ngcontent-%COMP%]   .separate[_ngcontent-%COMP%]{color:#00000073;line-height:22px}nz-content[_ngcontent-%COMP%]{padding:32px 86px 14px}#dev-menu[_ngcontent-%COMP%]{margin-left:auto}"]}),o})(),children:[{path:"dashboard",loadChildren:()=>Promise.all([n.e(687),n.e(635),n.e(582),n.e(210),n.e(631)]).then(n.bind(n,2631)).then(o=>o.DashboardModule),data:{breadcrumb:"Dashboard"}},{path:"ingredients",loadChildren:()=>Promise.all([n.e(687),n.e(635),n.e(582),n.e(879),n.e(592),n.e(990)]).then(n.bind(n,1990)).then(o=>o.IngredientsModule)},{path:"recipes",loadChildren:()=>Promise.all([n.e(687),n.e(635),n.e(582),n.e(879),n.e(360)]).then(n.bind(n,6360)).then(o=>o.RecipesModule)},{path:"terms",loadChildren:()=>Promise.all([n.e(687),n.e(635),n.e(582),n.e(879),n.e(592),n.e(301)]).then(n.bind(n,7301)).then(o=>o.TermsModule)},{path:"posts",loadChildren:()=>Promise.all([n.e(687),n.e(635),n.e(582),n.e(879),n.e(562)]).then(n.bind(n,3562)).then(o=>o.PostsModule)},{path:"super-resolution",loadChildren:()=>Promise.all([n.e(687),n.e(910),n.e(651)]).then(n.bind(n,2031)).then(o=>o.SuperResolutionModule)}]}];let A=(()=>{class o{}return o.\u0275fac=function(d){return new(d||o)},o.\u0275mod=t.oAB({type:o}),o.\u0275inj=t.cJS({imports:[s.Bz.forChild(M),s.Bz]}),o})(),C=(()=>{class o{}return o.\u0275fac=function(d){return new(d||o)},o.\u0275mod=t.oAB({type:o}),o.\u0275inj=t.cJS({imports:[r.ez,A,c.lt,i.b1,z.PV,e.wm,a.ip]}),o})()}}]);