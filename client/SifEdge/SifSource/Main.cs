using System;

namespace SifSource
{

    class MainClass
    {
        public static void Main(string[] args)
        {
            Source source = new Source(
                args[0], args[1], args[2], args[3],
                args[4] == "true");
            //"http://ws13.dyndns.ws/sif", "Player 1", "sif-03", "analog2", false);
            Console.WriteLine(source.ToString());
        }
    }
}