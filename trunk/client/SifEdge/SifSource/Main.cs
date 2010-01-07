using System;

namespace SifSource
{
	class MainClass
	{
		public static void Main(string[] args)
		{
            Source source = new Source("http://ws12.dyndns.ws/sif", "Player 1", "sif-03", "analog2", false);
			Console.WriteLine(source.ToString());
		}
	}
}