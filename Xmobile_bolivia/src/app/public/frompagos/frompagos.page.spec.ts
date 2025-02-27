import { CUSTOM_ELEMENTS_SCHEMA } from '@angular/core';
import { async, ComponentFixture, TestBed } from '@angular/core/testing';

import { FrompagosPage } from './frompagos.page';

describe('FrompagosPage', () => {
  let component: FrompagosPage;
  let fixture: ComponentFixture<FrompagosPage>;

  beforeEach(async(() => {
    TestBed.configureTestingModule({
      declarations: [ FrompagosPage ],
      schemas: [CUSTOM_ELEMENTS_SCHEMA],
    })
    .compileComponents();
  }));

  beforeEach(() => {
    fixture = TestBed.createComponent(FrompagosPage);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
