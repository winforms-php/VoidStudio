public class VoidControlsParser
{
    public static void parseControls (string group, Control control)
    {
        WinForms_PHP.Program.eval ("$GLOBALS[\'__underConstruction\'][\'" + group + "\'][\'" + control.Name + "\'] = " + WinForms_PHP.Program.HashByObject (control) + ";");

        for (int i = 0; i < control.Controls.Count; ++i)
        {
            WinForms_PHP.Program.eval ("$GLOBALS[\'__underConstruction\'][\'" + group + "\'][\'" + control.Controls[i].Name + "\'] = " + WinForms_PHP.Program.HashByObject (control.Controls[i]) + ";");
        
            try
            {
                parseControls (group, (Control) control.Controls[i]);
            }

            catch (Exception) {}
        }
    }
}