!macro customInit
    ${If} ${FileExists} "$LOCALAPPDATA\Programs\Flexi Quotation\Flexi Quotation.exe"
        MessageBox MB_YESNO|MB_ICONQUESTION "A previous version of Flexi Quotation is already installed.$\n$\nReplace it with the new version?$\n$\n(Your data will be kept.)" IDYES done IDNO abort
        abort:
        Abort
        done:
    ${EndIf}
!macroend
